<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipSatuanharga;

/**
 * SakipSatuanhargaSearch represents the model behind the search form of `backend\models\SakipSatuanharga`.
 */
class SakipSatuanhargaSearch extends SakipSatuanharga
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsatuanharga_id'], 'integer'],
            [['kode_satuanharga', 'nama_satuanharga', 'satuanharga_isaktif'], 'safe'],
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
        $query = SakipSatuanharga::find();

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
            'refsatuanharga_id' => $this->refsatuanharga_id,
        ]);

        $query->andFilterWhere(['like', 'kode_satuanharga', $this->kode_satuanharga])
            ->andFilterWhere(['like', 'nama_satuanharga', $this->nama_satuanharga])
            ->andFilterWhere(['like', 'satuanharga_isaktif', $this->satuanharga_isaktif]);

        return $dataProvider;
    }
}
