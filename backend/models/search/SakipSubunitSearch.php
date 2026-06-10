<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipSubunit;

/**
 * SakipSubunitSearch represents the model behind the search form of `backend\models\SakipSubunit`.
 */
class SakipSubunitSearch extends SakipSubunit
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsubunit_id'], 'integer'],
            [['kode_subunit', 'nama_subunit', 'subunit_isaktif'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SakipSubunit::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refsubunit_id' => $this->refsubunit_id,
        ]);

        $query->andFilterWhere(['like', 'kode_subunit', $this->kode_subunit])
            ->andFilterWhere(['like', 'nama_subunit', $this->nama_subunit])
            ->andFilterWhere(['like', 'subunit_isaktif', $this->subunit_isaktif]);

        return $dataProvider;
    }
}
