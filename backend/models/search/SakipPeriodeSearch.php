<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipPeriode;

/**
 * SakipPeriodeSearch represents the model behind the search form of `backend\models\SakipPeriode`.
 */
class SakipPeriodeSearch extends SakipPeriode
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refperiode_id', 'periode'], 'integer'],
            [['periode_isaktif'], 'safe'],
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
        $query = SakipPeriode::find();

        // add conditions that should always apply here
        $query->with(['periode5Tahun']);

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
            'refperiode_id' => $this->refperiode_id,
            'periode' => $this->periode,
        ]);

        $query->andFilterWhere(['like', 'periode_isaktif', $this->periode_isaktif]);

        return $dataProvider;
    }
}
